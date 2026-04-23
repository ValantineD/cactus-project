<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Config\Definition\Exception\Exception;

#[Route('/admin/theme')]
final class ThemeController extends AbstractController
{
    #[Route(name: 'app_theme_index', methods: ['GET'])]
    public function index(ThemeRepository $themeRepository): Response
    {
        return $this->render('theme/index.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_theme_new', methods: ['GET', 'POST'])]
    public function new(
        Request                           $request,
        EntityManagerInterface            $entityManager,
        SluggerInterface                  $slugger,
        #[Autowire('%app.upload.theme%')] $themeDirectory
    ): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $iconFile = $form->get('iconFilename')->getData();

            if ($iconFile) {
                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '_' . uniqid() . '.' . $iconFile->guessExtension();


                try {
                    $iconFile->move($themeDirectory, $newFilename);
                } catch (FileException $e) {
                    throw new Exception($e->getMessage());
                }

                $theme->setIconFilename('uploads/theme/' . $newFilename);
            }

            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('app_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/new.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_theme_show', methods: ['GET'])]
    public function show(Theme $theme): Response
    {
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_theme_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                           $request,
        Theme                             $theme,
        EntityManagerInterface            $entityManager,
        SluggerInterface                  $slugger,
        #[Autowire('%app.upload.theme%')] $themeDirectory,
    ): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $iconFile = $form->get('iconFilename')->getData();

            if ($iconFile) {

                if ($theme->getIconFilename()) {
                    $oldIconPath = $this->getParameter('kernel.project_dir') . '/public/uploads/theme/' . $theme->getIconFilename();
                    if (file_exists($oldIconPath)) {
                        unlink($oldIconPath);
                    }
                }

                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '_' . uniqid() . '.' . $iconFile->guessExtension();


                try {
                    $iconFile->move($themeDirectory, $newFilename);
                } catch
                (FileException $e) {
                    throw new Exception($e->getMessage());
                }

                $theme->setIconFilename('uploads/theme/' . $newFilename);
            }

            $entityManager->persist($theme);

            $entityManager->flush();

            return $this->redirectToRoute('app_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }
}
